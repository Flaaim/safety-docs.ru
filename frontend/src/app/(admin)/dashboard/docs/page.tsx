import {cookies} from "next/headers";
import {getAllProducts} from "@api/product";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/ui/table";
import {ProductDTO} from "@/interfaces/product.interface";
import AddProductDialog from "@/components/Admin/Dashboard/Docs/add-product-dialog";
import EditProductDialog from "@/components/Admin/Dashboard/Docs/edit-product-dialog";


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
              <TableHead>Обновлен</TableHead>
              <TableHead>Файл</TableHead>
              <TableHead className="text-right">Действия</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.products.map((prod: ProductDTO) => (
              <TableRow key={prod.id}>
                <TableCell className="font-medium">{prod.name}</TableCell>
                <TableCell className="font-medium">{prod.cipher}</TableCell>
                <TableCell className="font-medium">{prod.amount}</TableCell>
                <TableCell className="font-medium">{prod.updatedAt}</TableCell>
                <TableCell className="font-medium">{prod.file}</TableCell>
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
