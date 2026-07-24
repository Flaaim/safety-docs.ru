const isServer = typeof window === "undefined";
const BASE_URL = isServer
  ? process.env.INTERNAL_BACKEND_URL || process.env.NEXT_PUBLIC_BACKEND_URL || "http://api"
  : process.env.NEXT_PUBLIC_BACKEND_URL || "http://localhost:8081";

type PaginationParams = {
  page?: number;
  limit?: number;
  search?: string;
};

export const API = {
  payment: {
    create: () => BASE_URL + `/v1/payments/process-payment`,
    getByToken: (token: string) => BASE_URL + `/v1/payments/get/${token}`,
  },
  direction: {
    getAll: () => BASE_URL + `/v1/directions`,
    add: () => BASE_URL + `/v1/directions`,
    getBySlug: (slug: string) => BASE_URL + `/v1/directions/s/${slug}`,
    update: (id: string) => BASE_URL + `/v1/directions/${id}`,
  },
  category: {
    /** Public: slug hierarchy matching /directions/[directionSlug] */
    getAllByDirectionSlug: (directionSlug: string) =>
      BASE_URL + `/v1/directions/s/${directionSlug}/categories`,
    getBySlugs: (directionSlug: string, categorySlug: string) =>
      BASE_URL + `/v1/directions/s/${directionSlug}/categories/s/${categorySlug}`,
    getAllByDirection: (directionId: string) =>
      BASE_URL + `/v1/directions/${directionId}/categories`,
    add: (directionId: string) => BASE_URL + `/v1/directions/${directionId}/categories`,
    getBySlug: (slug: string, directionId: string) =>
      BASE_URL + `/v1/directions/${directionId}/categories/s/${slug}`,
    update: (id: string, directionId: string) =>
      BASE_URL + `/v1/directions/${directionId}/categories/${id}`,
    delete: (id: string, directionId: string) =>
      BASE_URL + `/v1/directions/${directionId}/categories/${id}`,
    getAllChildrenCategories: () => BASE_URL + `/v1/children-categories`,
  },
  document: {
    /** Admin: paginated Template read-model list */
    getAllAdmin: (
      page: number,
      perPage: number,
      filters?: {
        directionId?: string;
        categoryId?: string;
        search?: string;
      }
    ) => {
      const params = new URLSearchParams({
        page: String(page),
        perPage: String(perPage),
      });
      if (filters?.directionId) params.set("directionId", filters.directionId);
      if (filters?.categoryId) params.set("categoryId", filters.categoryId);
      if (filters?.search) params.set("search", filters.search);
      return BASE_URL + `/v1/templates?${params.toString()}`;
    },
    /** Public: slug hierarchy matching /directions/.../[templateSlug] */
    getAllByCategorySlugs: (
      directionSlug: string,
      categorySlug: string,
      params?: PaginationParams
    ) => {
      const url = new URL(
        BASE_URL + `/v1/directions/s/${directionSlug}/categories/s/${categorySlug}/documents`
      );

      if (params?.page) url.searchParams.set("page", params.page.toString());
      if (params?.limit) url.searchParams.set("limit", params.limit.toString());
      if (params?.search) url.searchParams.set("search", params.search.toString());
      return url.toString();
    },

    getBySlugs: (directionSlug: string, categorySlug: string, templateSlug: string) =>
      BASE_URL +
      `/v1/directions/s/${directionSlug}/categories/s/${categorySlug}/documents/s/${templateSlug}`,
    /** Admin: bulk upload documents into a leaf category */
    bulkUpload: (directionId: string, categoryId: string) =>
      BASE_URL + `/v1/directions/${directionId}/categories/${categoryId}/documents/bulk`,
    preview: (documentId: string) => BASE_URL + `/v1/preview/${documentId}`,
  },
  distribution: {
    getContactFiles: (currentPage: number, perPage: number) =>
      BASE_URL + `/v1/distributions/contact-files?page=${currentPage}&perPage${perPage}`,
    uploadContacts: () => BASE_URL + `/v1/distributions/contact-files`,
    removeContactsFile: (fileId: string) => BASE_URL + `/v1/distributions/contact-files/${fileId}`,
    importContacts: () => BASE_URL + `/v1/distributions/import-contacts`,
    addNewProject: () => BASE_URL + `/v1/distributions/projects`,
    getAllProjects: () => BASE_URL + `/v1/distributions/projects`,
    deleteProject: (projectId: string) => BASE_URL + `/v1/distributions/projects/${projectId}`,
    getAllNewsLettersPaginated: (currentPage: number, perPage: number, archive: boolean) => {
      const params = new URLSearchParams({
        page: String(currentPage),
        perPage: String(perPage),
      });

      if (archive) {
        params.append("archive", "true");
      }

      return BASE_URL + `/v1/distributions/newsletters?${params.toString()}`;
    },
    draftNewsletter: () => BASE_URL + `/v1/distributions/newsletters`,
    launchNewsletter: () => BASE_URL + `/v1/distributions/newsletters/launch`,
    archiveNewsletter: (newsletter: string) =>
      BASE_URL + `/v1/distributions/newsletters/${newsletter}`,
  },
  parser: {
    launch: () => BASE_URL + `/v1/parser/launch`,
  },
  sitemap: {
    getDocuments: () => BASE_URL + `/v1/sitemap/documents`,
  },
  token: {
    create: () => BASE_URL + `/v1/auth/login`,
  },
};
