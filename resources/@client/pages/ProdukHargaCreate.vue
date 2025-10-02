<template>
  <div class="py-6 bg-slate-100 min-h-screen">
    <div class="intro-y grid grid-cols-12 gap-6 mt-8">
      <!-- Form Card: full-width -->
      <div class="col-span-12">
        <div class="p-6 box">
          <h2 class="text-lg font-medium mb-4">Tambah Harga</h2>
          <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>

          <!-- Add Row Button -->
          <div class="mb-4 flex justify-end">
            <Button variant="outline-secondary" @click="addRow" class="inline-flex items-center gap-2">
              <Lucide icon="Plus" class="w-4 h-4" />
              <span>Add Row</span>
            </Button>
          </div>

          <!-- Rows -->
          <div
            v-for="(row, i) in formRows"
            :key="i"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-6 border-b pb-4"
          >
            <div class="col-span-full flex justify-between items-center">
              <span class="font-semibold">Row {{ i + 1 }}</span>
              <Button
                v-if="formRows.length > 1"
                variant="danger"
                size="sm"
                @click="removeRow(i)"
                class="inline-flex items-center gap-2"
              >
                <Lucide icon="Trash2" class="w-4 h-4" />
                <span>Remove</span>
              </Button>
            </div>

            <div>
              <FormLabel :for="`periode_awal_${i}`">Periode Awal</FormLabel>
              <FormInput
                :id="`periode_awal_${i}`"
                type="date"
                v-model="row.periode_awal"
                class="w-full"
              />
            </div>

            <div>
              <FormLabel :for="`periode_akhir_${i}`">Periode Akhir</FormLabel>
              <FormInput
                :id="`periode_akhir_${i}`"
                type="date"
                v-model="row.periode_akhir"
                class="w-full"
              />
            </div>

            <div>
              <FormLabel :for="`cabang_${i}`">Cabang</FormLabel>
              <FormSelect
                :id="`cabang_${i}`"
                v-model="row.id_cabang"
                class="w-full"
              >
                <option disabled value="">-- Pilih Cabang --</option>
                <option
                  v-for="c in cabangs"
                  :key="c.id_cabang"
                  :value="c.id_cabang"
                >
                  {{ c.nama_cabang }}
                </option>
              </FormSelect>
            </div>

            <div>
              <FormLabel :for="`produk_${i}`">Produk</FormLabel>
              <FormSelect
                :id="`produk_${i}`"
                v-model="row.id_produk"
                class="w-full"
              >
                <option disabled value="">-- Pilih Produk --</option>
                <option
                  v-for="p in produks"
                  :key="p.id_produk"
                  :value="p.id_produk"
                >
                  {{ p.nama_produk }} — Uk: {{ p.ukuran?.nama_ukuran || '-' }} — Sat: {{ p.ukuran?.satuan?.nama_satuan || '-' }}
                </option>
              </FormSelect>
            </div>

            <div>
              <FormLabel :for="`hpl_${i}`">Harga Price List</FormLabel>
              <FormInput
                :id="`hpl_${i}`"
                type="text"
                :value="row.displayPriceList"
                @input="onPriceListInput(i, $event)"
                placeholder="0"
                class="w-full"
              />
            </div>

            <div>
              <FormLabel :for="`hbm_${i}`">Harga BM</FormLabel>
              <FormInput
                :id="`hbm_${i}`"
                type="text"
                :value="row.displayBm"
                @input="onBmInput(i, $event)"
                placeholder="0"
                class="w-full"
              />
            </div>

            <div class="col-span-full">
              <FormLabel :for="`catatan_${i}`">Catatan</FormLabel>
              <textarea
                :id="`catatan_${i}`"
                v-model="row.catatan"
                rows="2"
                class="w-full border rounded px-3 py-2"
                placeholder="Tambahkan catatan (opsional)"
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <Button variant="outline-secondary" @click="cancel" class="inline-flex items-center gap-2">
              <Lucide icon="X" class="w-4 h-4" />
              <span>Batal</span>
            </Button>
            <Button variant="primary" :loading="loading" @click="submitAll" class="inline-flex items-center gap-2">
              <Lucide v-if="!loading" icon="Save" class="w-4 h-4" />
              <span>Simpan</span>
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import Button from '@/components/Base/Button'
import { FormInput, FormSelect, FormLabel } from '@/components/Base/Form'
import Lucide from '@/components/Base/Lucide'
import { useRouter } from 'vue-router'

// router
const router = useRouter()

// untuk dropdown cabang & produk
const cabangs = ref<any[]>([])
const produks = ref<any[]>([])
onMounted(async () => {
  const [c, p] = await Promise.all([
    axios.get('/api/cabangs', { params: { per_page: 100 } }),
    axios.get('/api/produks', { params: { per_page: 100 } })
  ])
  cabangs.value = c.data.data
  produks.value = p.data.data
})

// ambil nama user untuk created_by
const currentUser = ref('')
onMounted(async () => {
  const { data } = await axios.get('/api/user')
  currentUser.value = data.name
})

// tipe satu baris form
interface Row {
  periode_awal: string
  periode_akhir: string
  id_cabang: number | ''
  id_produk: number | ''
  harga_price_list: number | null
  displayPriceList: string
  harga_bm: number | null
  displayBm: string
  catatan: string
  created_by: string
}

// state: array baris
const formRows = ref<Row[]>([
  {
    periode_awal: '',
    periode_akhir: '',
    id_cabang: '',
    id_produk: '',
    harga_price_list: null,
    displayPriceList: '',
    harga_bm: null,
    displayBm: '',
    catatan: '',
    created_by: currentUser.value,
  },
])

// tambah / hapus baris
function addRow() {
  formRows.value.push({
    periode_awal: '',
    periode_akhir: '',
    id_cabang: '',
    id_produk: '',
    harga_price_list: null,
    displayPriceList: '',
    harga_bm: null,
    displayBm: '',
    catatan: '',
    created_by: currentUser.value,
  })
}
function removeRow(i: number) {
  if (formRows.value.length > 1) {
    formRows.value.splice(i, 1)
  }
}

// helper format ribuan
function formatThousand(v: string) {
  return v.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

// event handler input harga price list
function onPriceListInput(i: number, e: Event) {
  const raw = (e.target as HTMLInputElement).value.replace(/\D/g, '')
  formRows.value[i].harga_price_list = raw ? parseInt(raw) : null
  formRows.value[i].displayPriceList = formatThousand(raw)
}

// event handler input harga BM
function onBmInput(i: number, e: Event) {
  const raw = (e.target as HTMLInputElement).value.replace(/\D/g, '')
  formRows.value[i].harga_bm = raw ? parseInt(raw) : null
  formRows.value[i].displayBm = formatThousand(raw)
}

const loading = ref(false)
const error   = ref('')

// VALIDASI: semua wajib kecuali catatan
function validateRows(): { ok: boolean; message?: string } {
  for (let i = 0; i < formRows.value.length; i++) {
    const r = formRows.value[i]
    const rowNo = i + 1

    if (!r.periode_awal) {
      return { ok: false, message: `Row ${rowNo}: Periode Awal wajib diisi` }
    }
    if (!r.periode_akhir) {
      return { ok: false, message: `Row ${rowNo}: Periode Akhir wajib diisi` }
    }
    // cek range tanggal
    if (new Date(r.periode_akhir) < new Date(r.periode_awal)) {
      return { ok: false, message: `Row ${rowNo}: Periode Akhir tidak boleh lebih awal dari Periode Awal` }
    }

    if (!r.id_cabang) {
      return { ok: false, message: `Row ${rowNo}: Cabang wajib dipilih` }
    }
    if (!r.id_produk) {
      return { ok: false, message: `Row ${rowNo}: Produk wajib dipilih` }
    }

    if (r.harga_price_list === null || r.harga_price_list <= 0) {
      return { ok: false, message: `Row ${rowNo}: Harga Price List wajib diisi dan harus lebih dari 0` }
    }
    if (r.harga_bm === null || r.harga_bm <= 0) {
      return { ok: false, message: `Row ${rowNo}: Harga BM wajib diisi dan harus lebih dari 0` }
    }
  }
  return { ok: true }
}

// kirim semua baris
async function submitAll() {
  error.value = ''

  // Jalankan validasi SweetAlert
  const v = validateRows()
  if (!v.ok) {
    return Swal.fire({
      icon: 'error',
      title: 'Validasi Gagal',
      text: v.message,
    })
  }

  loading.value = true
  try {
    await Promise.all(
      formRows.value.map(r =>
        axios.post('/api/produk-hargas', {
          periode_awal:     r.periode_awal,
          periode_akhir:    r.periode_akhir,
          id_cabang:        r.id_cabang,
          id_produk:        r.id_produk,
          harga_price_list: r.harga_price_list,
          harga_bm:         r.harga_bm,
          catatan:          r.catatan,
          created_by:       currentUser.value,
        })
      )
    )
    Swal.fire({
      icon: 'success',
      title: 'Data tersimpan',
      toast: true,
      position: 'top-end',
      timer: 1500,
      showConfirmButton: false
    })
    router.push({ name: 'produk-hargas' })
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Gagal menyimpan'
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: error.value
    })
  } finally {
    loading.value = false
  }
}

function cancel() {
  router.back()
}
</script>

<style scoped>
/* opsional: override styling */
</style>
