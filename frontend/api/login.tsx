import {API} from "@/app/api";
import {TokenInterface} from "@/interfaces/token.interface";

export async function getToken(login:string, password:string): Promise<TokenInterface> {
  const response = await fetch(API.token.create(), {
    method: "POST",
    body: JSON.stringify({login, password}),
    headers: {'Content-Type': 'application/json'}
  })
  if(!response.ok){
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Неверный логин или пароль`);
  }
  return response.json();
}
