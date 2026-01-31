import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";


export interface DownloadButtonClientProps  extends DetailedHTMLProps<HTMLAttributes<HTMLButtonElement>, HTMLButtonElement> {
  children: ReactNode
}
