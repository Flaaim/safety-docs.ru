
import {API} from "@/app/api";
import {ProductCollection, ProductDTO, ProductInfoData} from "@/interfaces/product.interface";


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
