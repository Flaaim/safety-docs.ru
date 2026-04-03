import {API} from "@/app/api";
import {TokenInterface} from "@/interfaces/token.interface";
import {apiFetch} from "@api/apiClient";

export async function getToken(login:string, password:string): Promise<TokenInterface> {
  return await apiFetch<TokenInterface>(API.token.create(), {
    method: "POST",
    body: JSON.stringify({login, password}),
  });
}
