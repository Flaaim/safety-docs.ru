import {JSX} from "react";
import {Roboto_Mono} from "next/font/google";
import {DefItem, Deflisttag, DownloadButton, Spantag} from "@/components";
import cn from "classnames";
import {ProductDTO} from "@/interfaces/product.interface";

const robotoMono = Roboto_Mono({
  variable: "--font-roboto-mono",
  subsets: ["cyrillic"]
});

export const ProductInfo = ({name, formattedPrice, updatedAt, totalDocuments, formatDocuments, id }: ProductDTO): JSX.Element => {

  return (<div className={cn(robotoMono.variable)}>
    <Deflisttag >
      <DefItem term='Название' definition={name} />
      <DefItem term='Стоимость' definition={formattedPrice} />
      <DefItem term='Обновлен' definition={new Date(updatedAt).toLocaleDateString('ru-RU')} />
      <DefItem term='Кол-во документов' definition={totalDocuments + ` шт.`} />
      <DefItem term='Формат файлов' definition={formatDocuments.join(', ') || ''} />
    </Deflisttag>

    <DownloadButton
      headline={name}
      productId={id}>
      <Spantag size='s'> Скачать </Spantag> <br />
      <Spantag appearance='bold' size='m'>RAR Архив</Spantag>
    </DownloadButton>
  </div>);
};
