from fastapi import FastAPI
from pydantic import BaseModel
from motor.motor_asyncio import AsyncIOMotorClient
import os

app = FastAPI()

# Koneksi ke MongoDB
MONGO_URI = os.getenv("DB_URI", "mongodb+srv://toriqdev_db_user:xefkok-roqXi4-tysrus@cluster0.fnzzo8z.mongodb.net/?appName=Cluster0")
client = AsyncIOMotorClient(MONGO_URI, tlsAllowInvalidCertificates=True)
db = client.classsync_db

hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']

class GenerateRequest(BaseModel):
    user_id: str

@app.post("/api/ai/generate")
async def generate_schedule(req: GenerateRequest):
    user_id = req.user_id
    trace = [{"type": "start", "title": "Mulai: Generate Jadwal Mingguan"}]
    
    # 1. Ambil Setting
    setting = await db.settings.find_one({"user_id": user_id}) or \
              {"istirahat_1_jam": True, "maks_3_kegiatan": False, "waktu_produktif": True}
        
    jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00']
    if not setting.get('waktu_produktif', True):
        jam_produktif.extend(['18:00', '19:00', '20:00', '21:00'])

    # 2. Ambil Data
    jadwal_tetap = await db.activities.find({"user_id": user_id, "tipe": "tetap", "$or": [{"status": "aktif"}, {"status": {"$exists": False}}]}).to_list(None)
    tugas_fleksibel = await db.activities.find({"user_id": user_id, "tipe": "fleksibel", "is_scheduled": False, "$or": [{"status": "aktif"}, {"status": {"$exists": False}}]}).to_list(None)

    if not tugas_fleksibel:
        return {"status": "info", "message": "Tidak ada tugas baru.", "trace": trace}

    # Urutan variabel (Heuristik)
    tugas_fleksibel.sort(key=lambda x: (hari_list.index(x.get('deadline')) if x.get('deadline') in hari_list else 999, -int(x.get('durasi', 0))))

    # Kalender Kosong
    kalender = {hari: {jam: None for jam in jam_produktif} for hari in hari_list}
    for keg in jadwal_tetap:
        start_int = int(keg['jam_mulai'][:2])
        end_int = int(keg['jam_selesai'][:2])
        for i in range(end_int - start_int):
            jam_format = f"{(start_int + i):02d}:00"
            if jam_format in kalender[keg['hari']]: kalender[keg['hari']][jam_format] = str(keg['_id'])

    visited_states = set()

    # Fungsi Validasi (Dengan Jeda Istirahat yang ketat)
    def is_valid(hari, jam_mulai, durasi):
        start_int = int(jam_mulai[:2])
        # Cek Bentrok
        for i in range(durasi):
            jam_cek = f"{(start_int + i):02d}:00"
            if jam_cek not in jam_produktif or kalender[hari][jam_cek] is not None: return False, f"Bentrok di {jam_cek}"
        
        # Cek Jeda Istirahat
        if setting.get('istirahat_1_jam', True):
            # Jeda SEBELUM (slot 1 jam sebelum mulai harus kosong/bukan kegiatan)
            jam_sebelum = f"{(start_int - 1):02d}:00"
            if jam_sebelum in jam_produktif and kalender[hari][jam_sebelum] is not None: return False, "Jeda sebelum"
            # Jeda SESUDAH (slot 1 jam setelah selesai harus kosong)
            jam_setelah = f"{(start_int + durasi):02d}:00"
            if jam_setelah in jam_produktif and kalender[hari][jam_setelah] is not None: return False, "Jeda sesudah"
        return True, "Valid"

    # Backtracking dengan visited_states untuk cegah spamming
    def backtrack(index):
        if index == len(tugas_fleksibel): return True
        
        tugas = tugas_fleksibel[index]
        trace.append({'type': 'variable', 'title': f'Variabel: {tugas["nama_kegiatan"]}', 'detail': f'Durasi: {tugas["durasi"]}j · Deadline: {tugas.get("deadline")}'})

        state_id = f"{index}_{str(kalender)}"
        if state_id in visited_states: return False
        visited_states.add(state_id)

        for hari in hari_list:
            for jam in jam_produktif:
                valid, reason = is_valid(hari, jam, int(tugas['durasi']))
                if not valid: trace.append({'type': 'check', 'status': 'fail', 'title': f'{hari} {jam}', 'detail': reason})
                
                if valid:
                    start_int = int(jam[:2])
                    for i in range(int(tugas['durasi'])): kalender[hari][f"{(start_int + i):02d}:00"] = str(tugas['_id'])
                    trace.append({'type': 'assign', 'title': f'Tempatkan "{tugas["nama_kegiatan"]}" di {hari} {jam}'})
                    
                    if backtrack(index + 1): return True
                    
                    # Backtrack (Undo)
                    for i in range(int(tugas['durasi'])): kalender[hari][f"{(start_int + i):02d}:00"] = None
                    trace.append({'type': 'backtrack', 'title': f'Backtrack: lepas "{tugas["nama_kegiatan"]}"'})

        return False

    if backtrack(0):
        trace.append({"type": "success", "title": "Jadwal optimal ditemukan!"})
        for tugas in tugas_fleksibel:
            for hari in hari_list:
                for jam in jam_produktif:
                    if kalender[hari][jam] == str(tugas['_id']):
                        start_int = int(jam[:2])
                        await db.activities.update_one({"_id": tugas['_id']}, {"$set": {"hari": hari, "jam_mulai": jam, "jam_selesai": f"{(start_int + int(tugas['durasi'])):02d}:00", "is_scheduled": True}})
                        break
        return {"status": "success", "message": "Jadwal optimal ditemukan!", "trace": trace}
    
    return {"status": "error", "message": "AI gagal mencari waktu luang.", "trace": trace}