export interface Product {
  name: string;
  cipher: string;
  filename: string;
  slug: string;
  totalDocuments: number;
  formatDocuments: string[];
}

export interface CreateProductDTO extends Product{
  amount: string,
  updatedAt: string
  file: File | null,

}

export interface UpdateProductDTO extends Product {
  id: string,
  amount: string,
  updatedAt: string
  file: File | null
}

export interface ProductDTO extends Product {
  id: string,
  formattedPrice: string,
  updatedAt: string
  file: string,
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
