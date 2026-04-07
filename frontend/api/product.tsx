
import {API} from "@/app/api";
import {
  CreateProductDTO, Product,
  ProductCollection,
  ProductDTO,
  ProductFreeCollection,
  UpdateProductDTO
} from "@/interfaces/product.interface";
import {apiFetch} from "@api/apiClient";


export async function getProductBySlug(slug: string): Promise<ProductDTO> {
  return await apiFetch<ProductDTO>(API.product.getBySlug(slug), {
    method: "GET",
  });
}

export async function getProductById(id: string): Promise<ProductDTO> {
  return await apiFetch<ProductDTO>(API.product.getById(id), {
    method: "GET"
  });
}
export async function getAllProducts(token: string | undefined): Promise<ProductCollection>{

  return await apiFetch<ProductCollection>(API.product.getAll(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  });
}

export async function getFreeProducts(token: string | undefined): Promise<ProductFreeCollection>{

  return await apiFetch(API.product.getAllFree(), {
    method: "GET",
    token,
    cache: token ? 'no-store' : 'force-cache'
  });
}

export async function addProduct(token:string | undefined, product:Partial<CreateProductDTO>): Promise<void>{
  const formData = handleFormData(product)

  return await apiFetch<void>(API.product.add(), {
    method: "POST",
    token: token,
    body: formData
  });
}

export async function updateProduct(token: string|undefined, product:Partial<UpdateProductDTO>):Promise<void> {

  const formData = handleFormData(product)

  const productId = product.id || '';

  return await apiFetch<void>(API.product.update(productId), {
    method: "POST",
    token: token,
    body: formData
  });
}


function handleFormData(product: Product): FormData {

  const formData = new FormData();

  Object.entries(product).forEach(([key, value]) => {
    if (value === undefined || value === null) {
      return;
    }
    if (value instanceof File) {
      formData.append(key, value);
    }else if (Array.isArray(value)) {
      value.forEach((item) => {
        formData.append(`${key}[]`, String(item));
      });
    }else {
      formData.append(key, String(value));
    }
  });

  return formData
}
