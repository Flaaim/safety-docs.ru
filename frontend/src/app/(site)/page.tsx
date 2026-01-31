import {
  Button,
  Htag,
  InputForm,
  LabelForm,
  Listtag,
  ProductModal,
  Ptag,
  Spantag,
  ProductForm,
} from "@/components";


export default function Home() {
  return (
    <div >
      <Htag tag='h2'>Полный комплект ЛНА по охране труда на 2026 год</Htag>
      <Ptag>
        Локальные нормативные акты (ЛНА) — это внутренние документы вашей организации, которые регламентируют порядок работы, распределяют обязанности и обеспечивают выполнение требований закона в сфере охраны труда.
      </Ptag>
      <Ptag>
        Разделяют обязательные ЛНА (их наличие прямо требуется законодательством) и рекомендуемые (которые работодатель вводит для эффективного управления и снижения рисков).
      </Ptag>
      <Listtag appearance='ul'>
        <span>Item 1</span>
        <span>Item 2</span>
      </Listtag>
      <Listtag appearance='ol'>
        <span>Item 1</span>
        <span>Item 2</span>
      </Listtag>
      <ProductForm headline='Получить полный комплект документов по охране труда на 2026 год'>
        <LabelForm forInput='email'  >Введите ваш email: </LabelForm>
          <InputForm type='email' name='email' value=''  id='email' placeholder='Email'/>
          <InputForm type='hidden' name='productId' value=''  id='productId' />
          <Button type='submit'>Получить</Button>

      </ProductForm>
      <Spantag size='s'>Приобретая образцы документов вы соглашаетесь с условиями использования</Spantag>

    </div>
  );
}
