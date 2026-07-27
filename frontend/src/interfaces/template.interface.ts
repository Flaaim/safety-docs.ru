export interface TemplateItem {
  id: string;
  name: string;
  filename: string;
  slug: string;
  amount: number;
  createdAt: string;
}

export interface PaginatedTemplates {
  items: TemplateItem[];
  totalCount: number;
  totalPages: number;
}

export interface TemplatePreview {
  html: string;
}

export interface RelatedTemplates {
  items: TemplateItem[];
}
