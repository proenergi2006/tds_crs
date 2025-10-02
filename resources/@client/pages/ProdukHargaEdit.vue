<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

import Button from '@/components/Base/Button'
import { FormInput, FormSelect, FormLabel } from '@/components/Base/Form'
import Lucide from '@/components/Base/Lucide'

const route = useRoute()
const router = useRouter()
const id = Number(route.params.id)

// state form utama
const form = reactive({
  id_produk_harga: id,
  periode_awal: '',
  periode_akhir: '',
  id_cabang: '',
  id_produk: '',
  harga_price_list: 0,
  harga_bm: 0,
  catatan: '',
  lastupdate_by: ''
})

// untuk men-display dengan ribuan
const displayPriceList = ref('')
const displayBm        = ref('')

// dropdown data
const cabangs = ref<any[]>([])
const produks = ref<any[]>([])

// user untuk lastupdate_by
const currentUser = ref('')

// ambil cabang & produk
async function fetchDropdowns() {
  try {
    const resC = await axios.get('/api/cabangs', { params: { per_page: 100 } })
    cabangs.value = resC.data.data || resC.data
  } catch {}
  try {
    const resP = await axios.get('/api/produks', { params: { per_page: 100 } })
    produks.value = resP.data.data || resP.data
  } catch {}
}

// helper: format angka ribuan
function toThousands(n: number|string) {
  const num = typeof n === 'string' ? Number(n) : n
  if (!num) return ''
  return Number(num).toLocaleString('id-ID')
}

// handler input harga price list
function onPriceListInput(e: Event) {
  const raw = (e.target as HTMLInputElement).value.replace(/[^\d]/g, '')
  displayPriceList.value = raw ? Number(raw).toLocaleString('id-ID') : ''
  form.harga_price_list = raw ? Number(raw) : 0
}
// handler input harga bm
function onBmInput(e: Event) {
  const raw = (e.target as HTMLInputElement).value.replace(/[^\d]/g, '')
  displayBm.value = raw ? Number(raw).toLocaleString('id-ID') : ''
  form.harga_bm = raw ? Number(raw) : 0
}

// VALIDASI: semua wajib kecuali catatan
function validate(): { ok: boolean; message?: string } {
  if (!form.periode_awal) return { ok: false, message: 'Periode Awal wajib diisi' }
  if (!form.periode_akhir) return { ok: false, message: 'Periode Akhir wajib diisi' }
  if (new Date(form.periode_akhir) < new Date(form.periode_awal)) {
    return { ok: false, message: 'Periode Akhir tidak boleh lebih awal dari Periode Awal' }
  }
  if (!form.id_cabang) return { ok: false, message: 'Cabang wajib dipilih' }
  if (!form.id_produk) return { ok: false, message: 'Produk wajib dipilih' }
  if (!form.harga_price_list || form.harga_price_list <= 0) {
    return { ok: false, message: 'Harga Price List wajib diisi dan harus lebih dari 0' }
  }
  if (!form.harga_bm || form.harga_bm <= 0) {
    return { ok: false, message: 'Harga BM wajib diisi dan harus lebih dari 0' }
  }
  return { ok: true }
}

// simpan perubahan
async function submit() {
  const v = validate()
  if (!v.ok) {
    return Swal.fire({
      icon: 'error',
      title: 'Validasi Gagal',
      text: v.message
    })
  }

  try {
    await axios.put(`/api/produk-hargas/${id}`, {
      periode_awal: form.periode_awal,
      periode_akhir: form.periode_akhir,
      id_cabang: form.id_cabang,
      id_produk: form.id_produk,
      harga_price_list: form.harga_price_list,
      harga_bm: form.harga_bm,
      catatan: form.catatan,
      lastupdate_by: currentUser.value
    })
    Swal.fire({
      icon: 'success',
      title: 'Harga diperbarui',
      toast: true,
      position: 'top-end',
      timer: 2000,
      showConfirmButton: false
    })
    router.push({ name: 'produk-hargas' })
  } catch (e: any) {
    Swal.fire('Error', e.response?.data?.message || 'Gagal update', 'error')
  }
}

onMounted(async () => {
  // load dropdown
  await fetchDropdowns()
  // ambil user
  try {
    const { data: u } = await axios.get('/api/user')
    currentUser.value = u.name
    form.lastupdate_by = u.name
  } catch {}
  // ambil data existing
  const { data } = await axios.get(`/api/produk-hargas/${id}`)
  Object.assign(form, data)
  // tampilkan ke input harga dengan format ribuan
  displayPriceList.value = toThousands(data.harga_price_list)
  displayBm.value        = toThousands(data.harga_bm)
})
</script>

<template>
  <div class="py-6 bg-slate-100 min-h-screen">
    <div class="intro-y grid grid-cols-12 gap-6 mt-8">
      <div class="col-span-12">
        <div class="p-6 box">
          <h2 class="text-lg font-medium mb-4">Edit Harga</h2>
          <div class="space-y-4">
            <div>
              <FormLabel htmlFor="periode_awal">Periode Awal</FormLabel>
              <FormInput id="periode_awal" type="date" v-model="form.periode_awal" />
            </div>
            <div>
              <FormLabel htmlFor="periode_akhir">Periode Akhir</FormLabel>
              <FormInput id="periode_akhir" type="date" v-model="form.periode_akhir" />
            </div>
            <div>
              <FormLabel htmlFor="cabang">Cabang</FormLabel>
              <FormSelect id="cabang" v-model="form.id_cabang">
                <option disabled value="">-- Pilih Cabang --</option>
                <option v-for="c in cabangs" :key="c.id_cabang" :value="c.id_cabang">
                  {{ c.nama_cabang }}
                </option>
              </FormSelect>
            </div>
            <div>
              <FormLabel htmlFor="produk">Produk</FormLabel>
              <FormSelect id="produk" v-model="form.id_produk">
                <option disabled value="">-- Pilih Produk --</option>
                <option v-for="p in produks" :key="p.id_produk" :value="p.id_produk">
                  {{ p.nama_produk }} – {{ p.ukuran?.nama_ukuran }} ({{ p.ukuran?.satuan?.nama_satuan }})
                </option>
              </FormSelect>
            </div>
            <div>
              <FormLabel htmlFor="price_list">Harga Price List</FormLabel>
              <FormInput
                id="price_list"
                type="text"
                v-model="displayPriceList"
                @input="onPriceListInput"
                class="text-right"
                placeholder="0"
              />
            </div>
            <div>
              <FormLabel htmlFor="bm">Harga BM</FormLabel>
              <FormInput
                id="bm"
                type="text"
                v-model="displayBm"
                @input="onBmInput"
                class="text-right"
                placeholder="0"
              />
            </div>
            <div>
              <FormLabel htmlFor="catatan">Catatan</FormLabel>
              <textarea
                id="catatan"
                v-model="form.catatan"
                rows="3"
                class="w-full border rounded px-3 py-2"
                placeholder="(opsional)"
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <Button variant="outline-secondary" @click="() => router.back()" class="inline-flex items-center gap-2">
              <Lucide icon="X" class="w-4 h-4" />
              <span>Cancel</span>
            </Button>
            <Button variant="primary" @click="submit" class="inline-flex items-center gap-2">
              <Lucide icon="Save" class="w-4 h-4" />
              <span>Update</span>
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
