import { SitemapDocumentItem } from "@/interfaces/sitemap.interface";
import { apiFetch } from "@api/apiClient";
import { API } from "@/app/api";

export async function getSitemapDocuments(): Promise<SitemapDocumentItem[]> {
  return await apiFetch<SitemapDocumentItem[]>(API.sitemap.getDocuments(), {
    method: "GET",
  });
}
