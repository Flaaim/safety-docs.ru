import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface TagProps extends DetailedHTMLProps<HTMLAttributes<HTMLDivElement>, HTMLDivElement> {
  children: ReactNode
  href?: string
}
