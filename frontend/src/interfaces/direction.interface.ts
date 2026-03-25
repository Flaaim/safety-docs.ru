export interface DirectionCollection {
  directions: DirectionDTO[];
  total: number
}

export interface DirectionDTO{
  id: string,
  title: string,
  description: string,
  text: string
  slug: string
  categories: CategoryDTO[]
}

export interface CategoryDTO {
  title: string,
  description: string,
  text: string
  slug: string
}


