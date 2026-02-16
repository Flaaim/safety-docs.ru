export const API = {
  product: {
    getById: (id: string) =>  process.env.NEXT_PUBLIC_BACKEND_URL + `/payment-service/products/get/${id}`,
    getBySlug: (slug: string) => process.env.NEXT_PUBLIC_BACKEND_URL + `/payment-service/products/get/${slug}`
  }
}
