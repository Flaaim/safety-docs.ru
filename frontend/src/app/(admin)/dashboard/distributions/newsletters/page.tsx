import {cookies} from "next/headers";
import {getAllNewslettersPaginated} from "@api/distribution";


export default async function NewslettersPage() {

  const cookieStore = await cookies();
  const token = cookieStore.get("admin_token")?.value;

  const data = await getAllNewslettersPaginated(token);
}
