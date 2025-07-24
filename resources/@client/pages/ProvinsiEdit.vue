<template>
    <div class="p-6 bg-slate-100 min-h-screen">
        <div class="intro-y grid grid-cols-12 gap-6 mt-8">
            <div class="col-span-12 lg:col-span-8 xl:col-span-6">
                <div class="p-6 box bg-white shadow rounded">
        <h2 class="text-lg font-medium mb-4">Edit Provinsi</h2>
        <p v-if="error" class="text-red-500 mb-3">{{ error }}</p>
  
        <div class="space-y-4">
          <div>
            <FormLabel htmlFor="nama">Nama Provinsi</FormLabel>
            <FormInput
              id="nama"
              v-model="form.nama_provinsi"
              placeholder="Masukkan nama provinsi"
            />
          </div>
        </div>
  
        <div class="mt-6 flex justify-end space-x-2">
          <Button variant="outline-secondary" @click="cancel">Cancel</Button>
          <Button variant="primary" :loading="loading" @click="submit">Update</Button>
        </div>
    </div>
</div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { reactive, ref, onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import Button from '@/components/Base/Button'
  import { FormInput, FormLabel } from '@/components/Base/Form'
  
  const route  = useRoute()
  const router = useRouter()
  const id     = Number(route.params.id)
  
  const form = reactive({
    nama_provinsi: '',
  })
  const loading = ref(false)
  const error   = ref('')
  
  onMounted(async () => {
    try {
      const { data } = await axios.get(`/api/provinsis/${id}`)
      form.nama_provinsi = data.nama_provinsi
    } catch (e:any) {
      Swal.fire('Error','Gagal memuat data','error')
      router.back()
    }
  })
  
  async function submit() {
    error.value = ''
    if (!form.nama_provinsi.trim()) {
      return Swal.fire('Error','Nama provinsi wajib diisi','error')
    }
    loading.value = true
    try {
      await axios.put(`/api/provinsis/${id}`, form)
      Swal.fire({ icon:'success', title:'Provinsi diperbarui', toast:true, position:'top-end', timer:1500 })
      router.push({ name: 'provinsi-list' })
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Gagal update'
    } finally {
      loading.value = false
    }
  }
  
  function cancel() {
    router.back()
  }
  </script>
  