const isServer = typeof window === 'undefined';
const BASE_URL = isServer
  ? (process.env.INTERNAL_BACKEND_URL || "http://api")
  : (process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8081");

export const API = {
  product: {
    getById: (id: string) =>  BASE_URL + `/v1/products/get/${id}`,
    getBySlug: (slug: string) => BASE_URL + `/v1/products/get/${slug}`
  },
  payment: {
    create: () => BASE_URL + `/v1/payments/process-payment`,
    getByToken: (token: string) => BASE_URL + `/v1/payments/get/${token}`
  },
  direction: {
    getAll: () => BASE_URL + `/v1/directions`,
    add: () => BASE_URL + `/v1/directions`,
    getBySlug: (slug: string) => BASE_URL + `/v1/directions/${slug}`
  },
  token: {
    create: () => BASE_URL + `/v1/auth/login`,
  }
}
