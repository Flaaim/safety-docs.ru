import {DirectionCollection, DirectionDTO} from "@/interfaces/direction.interface";
import {API} from "@/app/api";


export async function getDirectionBySlug(slug: string): Promise<DirectionDTO> {
  const response = await fetch(API.direction.getBySlug(slug), {
    method: 'GET',
    headers: {'Content-Type': 'application/json'}
  })
  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
  }
  return response.json();
}

export async function getAllDirections(token?:string): Promise<DirectionCollection> {
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.direction.getAll(), {
    method: "GET",
    headers: headers,
    cache: token ? 'no-store' : 'force-cache'
  })

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
  }
  return response.json();
}
