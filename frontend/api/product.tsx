
import {API} from "@/app/api";
import {ProductInfoData} from "@/interfaces/product.interface";


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
