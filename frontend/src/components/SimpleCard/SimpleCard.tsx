import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import Link from "next/link"
import {SimpleCardProps} from "./SimpleCard.props";
import {JSX} from "react";

export default function SimpleCard({title, short_description, link, description, icon }:SimpleCardProps): JSX.Element {
  return (
    <Card className="w-[350px]">
      <CardHeader>
        <CardTitle>{icon} {title} </CardTitle>
        <CardDescription>{short_description}</CardDescription>
      </CardHeader>
      <CardContent>
        <p>{description}</p>
      </CardContent>
      <CardFooter>
        <Link
          href={link}
          className="text-sm font-medium text-primary underline underline-offset-4"
        >
         Перейти
        </Link>
      </CardFooter>
    </Card>
  )
}
