import {apiFetch} from "@api/apiClient";
import {API} from "@/app/api";

export async function uploadContacts(token: string | undefined, formData: FormData): Promise<void>{

  return await apiFetch<void>(API.distribution.uploadContacts(), {
    method: "POST",
    token: token,
    body: formData,
  });

}
