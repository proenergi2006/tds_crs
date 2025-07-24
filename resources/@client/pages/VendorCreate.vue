<template>
    <div class="py-6 bg-slate-100 min-h-screen">
      <div class="intro-y grid grid-cols-12 gap-6 mt-8">
        <div class="col-span-8">
          <div class="p-5 box">
            <h2 class="text-lg font-medium mb-4">Add Vendor</h2>
            <p v-if="error" class="text-red-500 mb-3">{{ error }}</p>
            <div class="space-y-4">
              <div>
                <FormLabel for="nama">Nama Vendor</FormLabel>
                <FormInput
                  id="nama"
                  v-model="form.nama_vendor"
                  placeholder="Nama Vendor"
                />
              </div>
              <div>
                <FormLabel for="inisial">Inisial</FormLabel>
                <FormInput
                  id="inisial"
                  v-model="form.inisial"
                  placeholder="Inisial (max 10)"
                  maxlength="10"
                />
              </div>
              <div>
                <FormLabel for="catatan">Catatan</FormLabel>
                <textarea
                  id="catatan"
                  v-model="form.catatan"
                  rows="3"
                  class="w-full border rounded px-3 py-2"
                  placeholder="Catatan (opsional)"
                ></textarea>
              </div>
              <div class="flex items-center space-x-2">
                <FormSwitch>
                  <FormSwitch.Input type="checkbox" v-model="form.is_active"/>
                </FormSwitch>
                <span>Active</span>
              </div>
              <div>
                <FormLabel for="creator">Created By</FormLabel>
                <FormInput
                  id="creator"
                  v-model="form.created_by"
                  readonly
                  class="bg-gray-100 cursor-not-allowed"
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
  import { reactive, ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import { useAuthStore } from '@/stores/auth'
  import { FormInput, FormLabel, FormSwitch } from '@/components/Base/Form'
  import Button from '@/components/Base/Button'
  
  const router = useRouter()
  const auth   = useAuthStore()
  
  const form = reactive({
    nama_vendor: '',
    inisial:     '',
    catatan:     '',
    is_active:   true,
    created_by:  ''
  })
  
  const loading = ref(false)
  const error   = ref('')
  
  // ambil nama user lalu set created_by
  onMounted(() => {
    form.created_by = auth.user?.name || ''
  })
  
  async function submit() {
    error.value = ''
    if (!form.nama_vendor.trim())  return Swal.fire('Error','Nama Vendor wajib diisi','error')
    if (!form.inisial.trim())      return Swal.fire('Error','Inisial wajib diisi','error')
  
    loading.value = true
    try {
      await axios.post('/api/vendors', form)
      Swal.fire({ icon: 'success', title: 'Vendor berhasil dibuat', toast:true, position:'top-end', timer:1500 })
      router.push({ name: 'vendors-list' })
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
  