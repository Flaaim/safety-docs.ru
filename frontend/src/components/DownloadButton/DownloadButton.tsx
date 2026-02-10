import {JSX} from "react";
import {DownloadButtonProps} from "@/components/DownloadButton/DownloadButton.props";
import {DownloadButtonClient} from "./Client/DownloadButton.client";


export const DownloadButton = ({children, productId, headline}:DownloadButtonProps): JSX.Element => {
  return <DownloadButtonClient productId={productId} headline={headline}>{children}</DownloadButtonClient>
}
