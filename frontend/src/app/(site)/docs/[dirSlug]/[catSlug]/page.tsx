import { redirect } from "next/navigation";

export default async function CategoryPage({
  params,
}: {
  params: Promise<{ dirSlug: string; catSlug: string }>;
}) {
  const { dirSlug, catSlug } = await params;
  redirect(`/directions/${dirSlug}/${catSlug}`);
}
