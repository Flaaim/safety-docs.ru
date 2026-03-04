import {ReactNode} from "react";


export interface SimpleCardProps {
  title: string,
  short_description: string
  link: string
  description: string
  icon?: ReactNode
}
