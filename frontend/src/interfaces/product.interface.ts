export interface CreateProductDTO {
  id: string,
  name: string,
  cipher: string,
  amount: string,
  path: string,
  updatedAt: string
  slug: string,
  file: File | null
}
export interface ProductDTO {
  id: string,
  name: string,
  cipher: string,
  amount: string,
  path: string,
  updatedAt: string
  slug: string,
  file: string
}
export interface ProductCollection {
  products: ProductDTO[]
  total: number
}
