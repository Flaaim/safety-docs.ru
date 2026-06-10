import {apiFetch} from "@api/apiClient";
import {API} from "@/app/api";
import {FilesCollection, ProjectsCollection} from "@/interfaces/distribution.interface";

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

export async function addNewProject(token: string | undefined, formData: FormData): Promise<void> {
  return await apiFetch<void>(API.distribution.addNewProject(), {
    method: "POST",
    token: token,
    body: formData,
  });
}
export async function getAllProjects(token: string | undefined): Promise<ProjectsCollection> {
  return await apiFetch<ProjectsCollection>(API.distribution.getAllProjects(), {
    method: "GET",
    token: token,
  })
}

export async function importContacts(token: string | undefined, projectId: string, fileId: string): Promise<void> {

  return await apiFetch<void>(API.distribution.importContacts(), {
    method: "POST",
    token: token,
    body: JSON.stringify({projectId: projectId, fileId: fileId})
  })

}
