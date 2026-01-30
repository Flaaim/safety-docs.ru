import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface DeflisttagProps extends DetailedHTMLProps<HTMLAttributes<HTMLDListElement>, HTMLDListElement> {
  children: ReactNode
}
