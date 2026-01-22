import {Ptag} from "@/components";


export default function Home() {
  return (
    <div >
      <Ptag   >Стандартный размер шрифта</Ptag>
      <Ptag  size='s'>Маленький размер шрифта</Ptag>
      <Ptag  appearance='bold'>Жирный шрифт</Ptag>
      <Ptag  appearance='italic' size='s'>Италик шрифт</Ptag>
      <Ptag  appearance='strikethrough'>Зачеркнутый шрифт</Ptag>
    </div>
  );
}
