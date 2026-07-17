/**
 * Template (Document) — matches App\Template\Query\Document DTOs.
 * Ubiquitous language: Template; API resource: Document.
 */
export interface Template {
  id: string;
  name: string;
  amount: number;
  filename: string;
  slug: string;
  createdAt: string;
}

export type TemplateDTO = Template;

/** Admin read-model row from GET /v1/templates */
export interface TemplateRow {
  id: string;
  name: string;
  directionName: string;
  categoryName: string;
  createdAt: string;
  status: string;
}

export interface TemplateListResult {
  templates: TemplateRow[];
  total: number;
  currentPage: number;
  perPage: number;
  totalPages: number;
}

export interface TemplateCollection {
  templates: Template[];
  total: number;
}

/** POST .../documents/bulk → EmptyResponse 201 (no body) */
export type BulkUploadResponse = void;

export interface BulkUploadValidationError {
  errors: Record<string, string>;
}

export interface BulkUploadDomainError {
  message: string;
}
