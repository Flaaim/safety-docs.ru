import { render, screen } from '@testing-library/react'
import App from './App'
import {describe} from "vitest";

describe('App', () => {
  it('renders the app title', () => {
    render(<App />)

    expect(screen.getByText(/vite \+ react/i)).toBeInTheDocument()
  })
})
