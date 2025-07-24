<template>
    <div class="py-6 bg-slate-100 min-h-screen">
      <div class="intro-y grid grid-cols-12 gap-6 mt-8">
        <!-- full width on small, half width on lg+ -->
        <div class="col-span-12 lg:col-span-8 xl:col-span-6">
          <div class="box p-5">
            <h2 class="text-lg font-medium mb-4">Add Provinsi</h2>
            <p v-if="error" class="text-red-500 mb-3">{{ error }}</p>
  
            <div class="space-y-4">
              <div>
                <FormLabel htmlFor="nama">Nama Provinsi</FormLabel>
                <FormInput
                  id="nama"
                  v-model="form.nama_provinsi"
                  placeholder="Masukkan nama provinsi"
                  class="w-full"
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
  import { reactive, ref } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  
  import Button from '@/components/Base/Button'
  import { FormInput, FormLabel } from '@/components/Base/Form'
  
  const router = useRouter()
  
  const form = reactive({
    nama_provinsi: '',
  })
  
  const loading = ref(false)
  const error   = ref('')
  
  async function submit() {
    error.value = ''
    if (!form.nama_provinsi.trim()) {
      return Swal.fire('Error','Nama provinsi wajib diisi','error')
    }
    loading.value = true
    try {
      await axios.post('/api/provinsis', form)
      Swal.fire({ icon:'success', title:'Provinsi dibuat', toast:true, position:'top-end', timer:1500 })
      router.push({ name: 'provinsi-list' })
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Gagal menyimpan'
    } finally {
      loading.value = false
    }
  }
  
  function cancel() {
    router.back()
  }
  </script>
  