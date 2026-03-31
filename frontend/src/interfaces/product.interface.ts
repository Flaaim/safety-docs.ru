export interface ProductDTO {
  id: string,
  name: string,
  cipher: string,
  price: string,
  updatedAt: string
  file: string,
}

export interface ProductCollection {
  products: ProductDTO[]
  total: number
}
