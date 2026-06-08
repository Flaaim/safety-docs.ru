import {cookies} from "next/headers";
import AddUploadContactsDialog from "@/components/Admin/Dashboard/Distributions/Import/add-upload-contacts-dialog";

export default async function ImportPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;


  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Импорт контактов</h1>
        <AddUploadContactsDialog />
      </div>
    </div>
  );
}
