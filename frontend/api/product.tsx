
import {API} from "@/app/api";
import {CreateProductDTO, ProductCollection, ProductFreeCollection} from "@/interfaces/product.interface";


export async function getProductBySlug(slug: string): Promise<ProductInfoData> {
  const response = await fetch(API.product.getBySlug(slug), {
    method: "GET",
    headers: {'Content-Type': 'application/json'},
  })
  if(!response.ok){
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`);
  }
  return response.json();
}
export async function getAllProducts(token: string | undefined): Promise<ProductCollection>{
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.product.getAll(), {
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

export async function getFreeProducts(token: string | undefined): Promise<ProductFreeCollection>{
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
  }

  if(token){
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.product.getAllFree(), {
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

export async function addProduct(token:string | undefined, product:Partial<CreateProductDTO>): Promise<void>{
  const formData = new FormData();

  Object.entries(product).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      formData.append(key, value instanceof File ? value : String(value));
    }
  });

  const headers: HeadersInit = {};

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(API.product.add(), {
    method: "POST",
    headers: headers,
    body: formData as BodyInit,
  });

  if (!response.ok) {
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка отправки данных`);
  }
}
