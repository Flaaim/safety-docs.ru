import {apiFetch} from "@api/apiClient";
import {API} from "@/app/api";
import {FilesCollection} from "@/interfaces/distribution.interface";

export async function uploadContacts(token: string | undefined, formData: FormData): Promise<void>{

  return await apiFetch<void>(API.distribution.uploadContacts(), {
    method: "POST",
    token: token,
    body: formData,
  });
}

export async function getContactFiles(token: string | undefined, currentPage: number, perPage: number): Promise<FilesCollection> {
  return await apiFetch<FilesCollection>(API.distribution.getContactFiles(currentPage, perPage), {
    method: "GET",
    token,
    cache: token ? "no-store" : "force-cache",
  })
}
export async function removeContactsFile(token: string | undefined, fileId: string): Promise<void> {
  return await apiFetch<void>(API.distribution.removeContactsFile(fileId), {
    method: "DELETE",
    token,
    cache: token ? "no-store" : "force-cache",
  });
}
