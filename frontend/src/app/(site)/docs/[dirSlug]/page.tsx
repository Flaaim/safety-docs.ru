import { redirect } from "next/navigation";

export default async function DirectionPage({ params }: { params: Promise<{ dirSlug: string }> }) {
  const { dirSlug } = await params;
  redirect(`/directions/${dirSlug}`);
}
