export interface CreateProductDTO {
  name: string,
  cipher: string,
  amount: string,
  path: string,
  updatedAt: string
  slug: string,
  file: File | null
}

export interface UpdateProductDTO {
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
  formattedPrice: string,
  path: string,
  updatedAt: string
  slug: string,
  file: string
}
export interface ProductCollection {
  products: ProductDTO[]
  total: number
}

export type ProductFreeCollection = ProductFree[];

export interface ProductFree {
  id: string,
  name: string
}
