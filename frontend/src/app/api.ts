export const API = {
  product: {
    getById: (id: string) =>  process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/products/get/${id}`,
    getBySlug: (slug: string) => process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/products/get/${slug}`
  },
  payment: {
    create: () => process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/payments/process-payment`,
    getByToken: (token: string) => process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/payments/get/${token}`
  },
  direction: {
    getAll: () => process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/directions`,
    getBySlug: (slug: string) => process.env.NEXT_PUBLIC_BACKEND_URL + `/v1/directions/get/${slug}`
  }
}
