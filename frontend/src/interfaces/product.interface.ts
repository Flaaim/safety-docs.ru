export interface Product {}

export interface CreateProductDTO extends Product{
  name: string,
  cipher: string,
  amount: string,
  filename: string,
  updatedAt: string
  slug: string,
  file: File | null,
  totalDocuments: number
  formatDocuments: string[]
}

export interface UpdateProductDTO extends Product {
  id: string,
  name: string,
  cipher: string,
  amount: string,
  filename: string
  updatedAt: string
  slug: string,
  file: File | null
  totalDocuments: number
  formatDocuments: string[]
}

export interface ProductDTO extends Product {
  id: string,
  name: string,
  cipher: string,
  amount: string,
  filename: string,
  updatedAt: string
  slug: string,
  file: string,
  totalDocuments: number,
  formatDocuments: string[]
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

export const formatsProduct = [
  "pdf",
  'docx',
  'doc',
  'excel'
] as const;
