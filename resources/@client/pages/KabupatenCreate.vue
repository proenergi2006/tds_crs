<template>
    <div class="py-6 bg-slate-100 min-h-screen">
    <div class="intro-y grid grid-cols-12 gap-6 mt-8">
        <div class="col-span-12 lg:col-span-8 xl:col-span-6">
            <div class="box p-5">
        <h2 class="text-lg font-medium mb-4">Add Kabupaten</h2>
        <p v-if="error" class="text-red-500 mb-3">{{ error }}</p>
  
        <div class="space-y-4">
          <div>
            <FormLabel htmlFor="provinsi">Provinsi</FormLabel>
            <FormSelect id="provinsi" v-model="form.id_provinsi">
              <option disabled value="">-- Pilih Provinsi --</option>
              <option
                v-for="p in provinsis"
                :key="p.id_provinsi"
                :value="p.id_provinsi"
              >
                {{ p.nama_provinsi }}
              </option>
            </FormSelect>
          </div>
  
          <div>
            <FormLabel htmlFor="nama">Nama Kabupaten</FormLabel>
            <FormInput
              id="nama"
              v-model="form.nama_kabupaten"
              placeholder="Masukkan nama kabupaten"
            />
          </div>
        </div>
  
        <div class="mt-6 flex justify-end space-x-2">
          <Button variant="outline-secondary" @click="cancel">Cancel</Button>
          <Button variant="primary" :loading="loading" @click="submit">Save</Button>
        </div>
    </div>
      </div>
    </div>
</div>
  </template>
  
  <script setup lang="ts">
  import { ref, reactive, onMounted } from 'vue'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import { useRouter } from 'vue-router'
  import Button from '@/components/Base/Button'
  import { FormInput, FormSelect, FormLabel } from '@/components/Base/Form'
  
  const router     = useRouter()
  const provinsis  = ref<any[]>([])
  const form       = reactive({
    id_provinsi:    '',
    nama_kabupaten: '',
  })
  const loading = ref(false)
  const error   = ref('')
  
  onMounted(async () => {
    // fetch provinsi untuk dropdown
    try {
      const res = await axios.get('/api/provinsis', { params:{ per_page:100 }})
      provinsis.value = res.data.data || res.data
    } catch {}
  })
  
  async function submit() {
    error.value = ''
    if (!form.id_provinsi) {
      return Swal.fire('Error','Provinsi wajib dipilih','error')
    }
    if (!form.nama_kabupaten.trim()) {
      return Swal.fire('Error','Nama kabupaten wajib diisi','error')
    }
    loading.value = true
    try {
      await axios.post('/api/kabupatens', form)
      Swal.fire({ icon:'success', title:'Kabupaten dibuat', toast:true, position:'top-end', timer:1500 })
      router.push({ name: 'kabupatens-list' })
    } catch (e:any) {
      error.value = e.response?.data?.message || 'Gagal menyimpan'
    } finally {
      loading.value = false
    }
  }
  
  function cancel() {
    router.back()
  }
  </script>
  