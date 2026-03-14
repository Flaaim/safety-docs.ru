export interface DirectionCollection {
  directions: DirectionDTO[];
  total: number
}


export interface DirectionDTO{
  title: string,
  description: string,
  text: string
  slug: string
}


