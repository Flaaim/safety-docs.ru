import { Template, TemplateListResult, BulkUploadResponse } from "@/types/template";
import { API } from "@/app/api";
import { apiFetch } from "@api/apiClient";

const publicRevalidate = 3600;

const ACCEPTED_EXTENSIONS = [".doc", ".docx"];
const ACCEPTED_MIME_TYPES = [
  "application/msword",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];

export function isAcceptedTemplateFile(file: File): boolean {
  const lowerName = file.name.toLowerCase();
  const hasExtension = ACCEPTED_EXTENSIONS.some((ext) => lowerName.endsWith(ext));
  const hasMime = file.type === "" || ACCEPTED_MIME_TYPES.includes(file.type);

  return hasExtension && hasMime;
}

/** Admin: bulk upload with upload progress (FormData) */
export function uploadTemplatesBulk(
  token: string | undefined,
  directionId: string,
  categoryId: string,
  amount: number,
  files: File[],
  onProgress?: (percent: number) => void
): Promise<BulkUploadResponse> {
  const formData = new FormData();
  formData.append("amount", String(amount));
  files.forEach((file) => formData.append("files[]", file));

  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", API.document.bulkUpload(directionId, categoryId));

    if (token) {
      xhr.setRequestHeader("Authorization", `Bearer ${token}`);
    }

    xhr.upload.onprogress = (event) => {
      if (event.lengthComputable && onProgress) {
        onProgress(Math.round((event.loaded / event.total) * 100));
      }
    };

    xhr.onload = () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        resolve();
        return;
      }

      try {
        const data = JSON.parse(xhr.responseText) as {
          message?: string;
          errors?: Record<string, string>;
        };

        if (data.errors) {
          reject(new Error(Object.values(data.errors).join(". ")));
          return;
        }

        reject(new Error(data.message ?? "Ошибка загрузки документов"));
      } catch {
        reject(new Error("Ошибка загрузки документов"));
      }
    };

    xhr.onerror = () => reject(new Error("Сетевая ошибка при загрузке"));
    xhr.send(formData);
  });
}

/** Admin paginated Template list (Read Model) */
export async function getAdminTemplates(
  token: string | undefined,
  page: number,
  perPage: number,
  filters?: {
    directionId?: string;
    categoryId?: string;
    search?: string;
  }
): Promise<TemplateListResult> {
  return await apiFetch<TemplateListResult>(API.document.getAllAdmin(page, perPage, filters), {
    method: "GET",
    token,
    cache: "no-store",
  });
}

/** Preferred public API — mirrors /directions/.../[categorySlug] documents list */
export async function getTemplatesByCategorySlugs(
  directionSlug: string,
  categorySlug: string,
  token?: string
): Promise<Template[]> {
  return await apiFetch<Template[]>(
    API.document.getAllByCategorySlugs(directionSlug, categorySlug),
    {
      method: "GET",
      token,
      ...(token
        ? { cache: "no-store" }
        : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
    }
  );
}

/** Preferred public API — mirrors /directions/.../[templateSlug] */
export async function getTemplateBySlugs(
  directionSlug: string,
  categorySlug: string,
  templateSlug: string,
  token?: string
): Promise<Template> {
  return await apiFetch<Template>(
    API.document.getBySlugs(directionSlug, categorySlug, templateSlug),
    {
      method: "GET",
      token,
      ...(token
        ? { cache: "no-store" }
        : { next: { revalidate: publicRevalidate, tags: ["categories"] } }),
    }
  );
}
