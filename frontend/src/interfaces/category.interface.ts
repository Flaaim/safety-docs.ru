import {ProductDTO, ProductFree} from "@/interfaces/product.interface";

export interface CategoryCollection {
  categories: CategoryDTO[]
  total: number
}

export interface CategoryDTO {
  id: string,
  title: string,
  description: string,
  text: string
  slug: string
  directionTitle: string,
  directionId: string,
  product: ProductDTO | null
}

export interface AssignCategory {
  productId: string,
  categoryId: string
}
