const isServer = typeof window === 'undefined';
const BASE_URL = isServer
  ? (process.env.INTERNAL_BACKEND_URL || "http://api")
  : (process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8081");

export const API = {
  product: {
    getAll:() => BASE_URL + `/v1/products`,
    getAllFree: () => BASE_URL + `/v1/products/free`,
    add: () => BASE_URL + `/v1/products`,
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
    getBySlug: (slug: string) => BASE_URL + `/v1/directions/s/${slug}`,
    update: (id: string) => BASE_URL + `/v1/directions/${id}`
  },
  category: {
    getAll: () => BASE_URL + `/v1/categories`,
    add: (directionId: string) => BASE_URL + `/v1/directions/${directionId}/categories`,
    getBySlug: (slug: string, directionId: string) => BASE_URL + `/v1/directions/${directionId}/categories/s/${slug}`,
    update: (id: string , directionId: string) => BASE_URL + `/v1/directions/${directionId}/categories/${id}`,
    assignProduct: () => BASE_URL + `/v1/categories/assign`
  },
  token: {
    create: () => BASE_URL + `/v1/auth/login`,
  }
}
