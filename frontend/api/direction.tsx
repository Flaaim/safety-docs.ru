import { Direction, DirectionWithCategories } from "@/types/direction";
import { API } from "@/app/api";
import { apiFetch } from "@api/apiClient";

const publicCache: RequestCache = "no-store";

export async function getDirectionBySlug(
  slug: string,
  token?: string
): Promise<DirectionWithCategories> {
  return await apiFetch<DirectionWithCategories>(API.direction.getBySlug(slug), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
  });
}

export async function getAllDirections(token?: string): Promise<Direction[]> {
  return await apiFetch<Direction[]>(API.direction.getAll(), {
    method: "GET",
    token,
    cache: token ? "no-store" : publicCache,
  });
}

export async function addDirection(
  token: string | undefined,
  direction: Partial<Direction>
): Promise<void> {
  return await apiFetch<void>(API.direction.add(), {
    method: "POST",
    token,
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
    }),
  });
}

export async function updateDirection(
  token: string | undefined,
  id: string,
  direction: Partial<Direction>
): Promise<void> {
  return await apiFetch<void>(API.direction.update(id), {
    method: "PUT",
    token,
    body: JSON.stringify({
      title: direction.title,
      description: direction.description,
      text: direction.text,
    }),
  });
}
