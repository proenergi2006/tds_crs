// resources/@client/stores/auth.ts
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as { id:string;name: string; email: string;  id_role: number;  two_factor_secret: string | null } | null,
  }),
  actions: {
    async fetchUser() {
      try {
        const { data } = await axios.get('/api/user')
        this.user = data
      } catch {
        this.user = null
      }
    },
    clear() {
      this.user = null
      localStorage.removeItem('access_token')
      delete axios.defaults.headers.common['Authorization']
    }
  }
})
