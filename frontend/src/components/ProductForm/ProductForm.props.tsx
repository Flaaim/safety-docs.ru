import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";




export interface ProductFormProps extends DetailedHTMLProps<HTMLAttributes<HTMLFormElement>, HTMLFormElement>{
  children: ReactNode,
  headline: ReactNode
}
