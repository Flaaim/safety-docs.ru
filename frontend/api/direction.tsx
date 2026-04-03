import {DirectionCollection, DirectionDTO} from "@/interfaces/direction.interface";
import {API} from "@/app/api";
import {apiFetch} from "@api/apiClient";

export async function getDirectionBySlug(slug: string, token?: string): Promise<DirectionDTO> {

  return await apiFetch<DirectionDTO>(API.direction.getBySlug(slug), {
    method: 'GET',
    token,
    cache: token ? 'no-store' : 'force-cache'
  });
}

export async function getAllDirections(token?: string): Promise<DirectionCollection> {

  return await apiFetch<DirectionCollection>(API.direction.getAll(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  });
}

export async function addDirection(token: string | undefined, direction: Partial<DirectionDTO>): Promise<void> {

  return await apiFetch<void>(API.direction.add(), {
    method: "POST",
    token,
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
      slug: direction.slug
    })
  });
}

export async function updateDirection(token: string | undefined, id: string, direction: Partial<DirectionDTO>): Promise<void> {

  return await apiFetch<void>(API.direction.update(id), {
    method: 'PUT',
    token,
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
      slug: direction.slug
    })
  });
}
