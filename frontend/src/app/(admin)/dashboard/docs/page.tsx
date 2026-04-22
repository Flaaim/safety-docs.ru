import {cookies} from "next/headers";
import {getAllProducts} from "@api/product";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {ProductDTO} from "@/interfaces/product.interface";
import AddProductDialog from "@/components/Admin/Dashboard/Docs/add-product-dialog";
import EditProductDialog from "@/components/Admin/Dashboard/Docs/edit-product-dialog";
import AddImagesDialog from "@/components/Admin/Dashboard/Docs/Images/add-images-dialog";
import ClearImagesDialog from "@/components/Admin/Dashboard/Docs/Images/clear-images-dialog";
import Image from 'next/image';

export default async function ProductPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getAllProducts(token);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Подборки документов</h1>
        <AddProductDialog></AddProductDialog>
      </div>
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Шифр</TableHead>
              <TableHead>Цена</TableHead>
              <TableHead>Галерея</TableHead>
              <TableHead>Обновлен</TableHead>
              <TableHead>Имя файла</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.products.map((prod: ProductDTO) => (
              <TableRow key={prod.id}>
                <TableCell className="font-medium">{prod.name}</TableCell>
                <TableCell className="font-medium">{prod.cipher}</TableCell>
                <TableCell className="font-medium">{prod.formattedPrice}</TableCell>
                <TableCell className="font-medium">
                  <div className="flex items-center gap-2">
                    {prod.images.length > 0 ? (
                      <>
                      <div className="flex -space-x-2">
                        {prod.images.slice(0, 3).map((image, idx) => (
                          <Image
                            key={idx}
                            src={`${process.env.NEXT_PUBLIC_BACKEND_URL}/images/${prod.id}/${image}`}
                            alt=""
                            width='10'
                            height='10'
                            className="w-8 h-8 rounded border-2 border-white object-cover"
                          />
                        ))}
                      </div>
                      {prod.images.length > 3 && (
                        <span className="text-xs text-gray-500">
                        +{prod.images.length - 3}
                      </span>)}
                      {<ClearImagesDialog  productId={prod.id}/>}

                      </>
                      ) : (<><span className="text-gray-400 text-sm">Нет фото</span>{<AddImagesDialog productId={prod.id} />}</>)}
                  </div>
                </TableCell>
                <TableCell className="font-medium">{prod.updatedAt}</TableCell>
                <TableCell className="font-medium">{prod.filename}</TableCell>
                <TableCell className="text-right">
                  <EditProductDialog productId={prod.id}></EditProductDialog>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
