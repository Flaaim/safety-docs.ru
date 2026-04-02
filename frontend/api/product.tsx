
import {API} from "@/app/api";
import {CreateProductDTO, ProductCollection, ProductDTO, ProductFreeCollection} from "@/interfaces/product.interface";
import {apiFetch} from "@api/apiClient";


export async function getProductBySlug(slug: string): Promise<ProductDTO> {
  return await apiFetch<ProductDTO>(API.product.getBySlug(slug), {
    method: "GET",
  })
}

export async function getProductById(id: string): Promise<ProductDTO> {
  return await apiFetch<ProductDTO>(API.product.getById(id), {
    method: "GET"
  })
}
export async function getAllProducts(token: string | undefined): Promise<ProductCollection>{

  return await apiFetch<ProductCollection>(API.product.getAll(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  })
}

export async function getFreeProducts(token: string | undefined): Promise<ProductFreeCollection>{

  return await apiFetch(API.product.getAllFree(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  })
}

export async function addProduct(token:string | undefined, product:Partial<CreateProductDTO>): Promise<void>{
  const formData = new FormData();

  Object.entries(product).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      formData.append(key, value instanceof File ? value : String(value));
    }
  });


  return await apiFetch<void>(API.product.add(), {
    method: "POST",
    token: token,
    body: formData
  });
}
