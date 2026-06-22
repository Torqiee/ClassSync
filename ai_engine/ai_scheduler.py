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
    
    # 1. Ambil Setting dari MongoDB
    setting = await db.settings.find_one({"user_id": user_id}) or {}
    
    # Mapping preferensi user ke variabel lokal dengan default value
    istirahat_1_jam = setting.get('istirahat_1_jam', True)
    maks_3_kegiatan = setting.get('maks_3_kegiatan', False)
    waktu_produktif = setting.get('waktu_produktif', True)
    no_overlap_all = setting.get('no_overlap_all', True)
    no_overlap_kuliah = setting.get('no_overlap_kuliah', True)
    strict_deadline = setting.get('strict_deadline', True)
    prioritas_kuliah = setting.get('prioritas_kuliah', True)

    jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00']
    if not waktu_produktif:
        jam_produktif.extend(['18:00', '19:00', '20:00', '21:00'])

    # 2. Ambil Data Jadwal
    jadwal_tetap = await db.activities.find({"user_id": user_id, "tipe": "tetap", "$or": [{"status": "aktif"}, {"status": {"$exists": False}}]}).to_list(None)
    tugas_fleksibel = await db.activities.find({"user_id": user_id, "tipe": "fleksibel", "is_scheduled": False, "$or": [{"status": "aktif"}, {"status": {"$exists": False}}]}).to_list(None)

    if not tugas_fleksibel:
        return {"status": "info", "message": "Tidak ada tugas baru.", "trace": trace}

    # PRIORITAS KULIAH: Jika aktif, tugas kategori 'Kuliah' akan ditaruh di urutan pertama array (Prioritas Backtracking)
    tugas_fleksibel.sort(key=lambda x: (
        0 if prioritas_kuliah and x.get('kategori') == 'Kuliah' else 1,
        hari_list.index(x.get('deadline')) if x.get('deadline') in hari_list else 999,
        -int(x.get('durasi', 0))
    ))

    # Bangun Kalender - Sekarang nyimpen dict berisi id dan kategori
    kalender = {hari: {jam: None for jam in jam_produktif} for hari in hari_list}
    
    for keg in jadwal_tetap:
        start_int = int(keg['jam_mulai'][:2])
        end_int = int(keg['jam_selesai'][:2])
        for i in range(end_int - start_int):
            jam_format = f"{(start_int + i):02d}:00"
            if jam_format in kalender[keg['hari']]: 
                kalender[keg['hari']][jam_format] = {
                    'id': str(keg['_id']), 
                    'kategori': keg.get('kategori', '')
                }

    visited_states = set()

    # Fungsi Validasi Super Dinamis
    def is_valid(hari, jam_mulai, durasi, tugas):
        start_int = int(jam_mulai[:2])
        
        # CONSTRAINT 1: STRICT DEADLINE
        if strict_deadline:
            dl = tugas.get('deadline')
            if dl in hari_list and hari_list.index(hari) > hari_list.index(dl):
                return False, f"Lewat deadline ({dl})"

        # CONSTRAINT 2: MAKS 3 KEGIATAN
        if maks_3_kegiatan:
            kegiatan_hari_ini = set()
            for j in jam_produktif:
                if kalender[hari][j] is not None:
                    kegiatan_hari_ini.add(kalender[hari][j]['id'])
            # Jika sudah ada 3 tugas unik, dan ini adalah tugas baru
            if len(kegiatan_hari_ini) >= 3 and str(tugas['_id']) not in kegiatan_hari_ini:
                return False, "Maks 3 kegiatan per hari penuh"

        # CONSTRAINT 3: BENTROK (Overlap)
        for i in range(durasi):
            jam_cek = f"{(start_int + i):02d}:00"
            if jam_cek not in jam_produktif: return False, "Luar jam operasional"
            
            slot_terisi = kalender[hari][jam_cek]
            if slot_terisi is not None:
                # Jika aturan 'Tidak boleh ada dua kegiatan' aktif
                if no_overlap_all: 
                    return False, f"Bentrok di {jam_cek}"
                
                # Jika overlap diizinkan, tapi 'Tidak boleh bentrok dengan kuliah' aktif
                if no_overlap_kuliah:
                    if slot_terisi['kategori'] == 'Kuliah' or tugas.get('kategori') == 'Kuliah':
                        return False, f"Tumpang tindih dgn kuliah di {jam_cek}"

        # CONSTRAINT 4: JEDA ISTIRAHAT
        if istirahat_1_jam:
            jam_sebelum = f"{(start_int - 1):02d}:00"
            if jam_sebelum in jam_produktif and kalender[hari][jam_sebelum] is not None: 
                return False, "Jeda istirahat kurang"
            jam_setelah = f"{(start_int + durasi):02d}:00"
            if jam_setelah in jam_produktif and kalender[hari][jam_setelah] is not None: 
                return False, "Jeda istirahat kurang"
                
        return True, "Valid"

    def backtrack(index):
        if index == len(tugas_fleksibel): return True
        
        tugas = tugas_fleksibel[index]
        trace.append({'type': 'variable', 'title': f'Variabel: {tugas["nama_kegiatan"]}', 'detail': f'Durasi: {tugas["durasi"]}j · Deadline: {tugas.get("deadline")}'})

        state_id = f"{index}_{str(kalender)}"
        if state_id in visited_states: return False
        visited_states.add(state_id)

        for hari in hari_list:
            for jam in jam_produktif:
                valid, reason = is_valid(hari, jam, int(tugas['durasi']), tugas)
                if not valid: trace.append({'type': 'check', 'status': 'fail', 'title': f'{hari} {jam}', 'detail': reason})
                
                if valid:
                    start_int = int(jam[:2])
                    # ASSIGN
                    for i in range(int(tugas['durasi'])): 
                        kalender[hari][f"{(start_int + i):02d}:00"] = {
                            'id': str(tugas['_id']), 
                            'kategori': tugas.get('kategori', '')
                        }
                    trace.append({'type': 'assign', 'title': f'Tempatkan "{tugas["nama_kegiatan"]}" di {hari} {jam}'})
                    
                    if backtrack(index + 1): return True
                    
                    # UNDO (Backtrack)
                    for i in range(int(tugas['durasi'])): 
                        kalender[hari][f"{(start_int + i):02d}:00"] = None
                    trace.append({'type': 'backtrack', 'title': f'Backtrack: lepas "{tugas["nama_kegiatan"]}"'})

        return False

    if backtrack(0):
        trace.append({"type": "success", "title": "Jadwal optimal ditemukan!"})
        for tugas in tugas_fleksibel:
            for hari in hari_list:
                for jam in jam_produktif:
                    # Update pencarian ID karena kalender sekarang dict
                    if kalender[hari][jam] is not None and kalender[hari][jam]['id'] == str(tugas['_id']):
                        start_int = int(jam[:2])
                        await db.activities.update_one({"_id": tugas['_id']}, {"$set": {"hari": hari, "jam_mulai": jam, "jam_selesai": f"{(start_int + int(tugas['durasi'])):02d}:00", "is_scheduled": True}})
                        break
        return {"status": "success", "message": "Jadwal optimal ditemukan!", "trace": trace}
    
    return {"status": "error", "message": "AI gagal mencari waktu luang.", "trace": trace}