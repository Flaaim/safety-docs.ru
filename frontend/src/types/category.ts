/** Matches App\Template\Query\Category\GetAllByDirection\CategoryDTO */
export interface Category {
  id: string;
  title: string;
  description: string;
  text: string;
  slug: string;
  parentId: string | null;
  directionId: string;
  children: Category[];
}

/** @deprecated Prefer Category */
export interface CategoryDTO extends Category {
  directionTitle?: string;
  productTitle?: string | null;
  productId?: string | null;
}

export interface CategoryCollection {
  categories: CategoryDTO[];
  total: number;
}
