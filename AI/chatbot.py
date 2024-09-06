import requests
import nltk
from nltk.tokenize import word_tokenize
import tkinter as tk
from tkinter import simpledialog, messagebox
from pyswip import Prolog

# Initialize Prolog
prolog = Prolog()
prolog.consult('knowledge_base.pl')

# Download NLTK data
nltk.download('punkt')

# Function to fetch current stock price from Prolog knowledge base
def get_stock_price_from_prolog(symbol):
    result = list(prolog.query(f'fact(stock_price({symbol}, Price))'))
    if result:
        return float(result[0]['Price'])
    return None

# Function to fetch current stock price from API
def get_stock_price_from_api(symbol, api_key):
    url = f'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol={symbol}&apikey={api_key}'
    try:
        response = requests.get(url)
        data = response.json()
        price = data['Global Quote']['05. price']
        return float(price)
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return None

# Function to calculate compound interest
def calculate_compound_interest(principal, rate, time):
    amount = principal * (1 + rate / 100) ** time
    interest = amount - principal
    return amount, interest

# Function to calculate simple interest
def calculate_simple_interest(principal, rate, time):
    interest = (principal * rate * time) / 100
    amount = principal + interest
    return amount, interest

# Function to convert currency using ExchangeRate-API
def convert_currency(amount, from_currency, to_currency, api_key):
    url = f'https://v6.exchangerate-api.com/v6/{api_key}/pair/{from_currency}/{to_currency}/{amount}'
    try:
        response = requests.get(url)
        data = response.json()
        if data['result'] == 'success':
            converted_amount = data['conversion_result']
            return converted_amount
        else:
            print(f"Error: {data['error-type']}")
            return None
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        return None

# Function to determine user intent
def determine_intent(message):
    tokens = word_tokenize(message.lower())
    if 'stock' in tokens and ('price' in tokens or 'prices' in tokens):
        return 'get_stock_price'
    elif 'compound' in tokens and 'interest' in tokens:
        return 'calculate_compound_interest'
    elif 'simple' in tokens and 'interest' in tokens:
        return 'calculate_simple_interest'
    elif 'convert' in tokens and 'currency' in tokens:
        return 'convert_currency'
    elif 'help' in tokens:
        return 'help'
    else:
        return 'unknown'

# Function to handle user input
def handle_user_input():
    user_input = entry.get()
    intent = determine_intent(user_input)
    
    if intent == 'get_stock_price':
        stock_symbol = simpledialog.askstring("Stock Price", "Which stock symbol?")
        if stock_symbol:
            price = get_stock_price_from_prolog(stock_symbol)
            if price is None:
                price = get_stock_price_from_api(stock_symbol, api_key_stock)
            if price is not None:
                result_text = f'Current price of {stock_symbol}: ${price:.2f}'
            else:
                result_text = f'Sorry, I couldn\'t find the price for {stock_symbol}.'
        else:
            result_text = 'Stock symbol not provided.'
    elif intent == 'calculate_compound_interest':
        principal = float(simpledialog.askstring("Compound Interest", "Enter principal amount:"))
        rate = float(simpledialog.askstring("Compound Interest", "Enter annual interest rate:"))
        time = int(simpledialog.askstring("Compound Interest", "Enter number of years:"))
        final_amount, interest = calculate_compound_interest(principal, rate, time)
        result_text = f'After {time} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})'
    elif intent == 'calculate_simple_interest':
        principal = float(simpledialog.askstring("Simple Interest", "Enter principal amount:"))
        rate = float(simpledialog.askstring("Simple Interest", "Enter annual interest rate:"))
        time = int(simpledialog.askstring("Simple Interest", "Enter number of years:"))
        final_amount, interest = calculate_simple_interest(principal, rate, time)
        result_text = f'After {time} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})'
    elif intent == 'convert_currency':
        amount = float(simpledialog.askstring("Currency Conversion", "Enter amount:"))
        from_currency = simpledialog.askstring("Currency Conversion", "Enter from currency (e.g., USD):")
        to_currency = simpledialog.askstring("Currency Conversion", "Enter to currency (e.g., EUR):")
        converted_amount = convert_currency(amount, from_currency, to_currency, api_key_currency)
        if converted_amount is not None:
            result_text = f'{amount} {from_currency} is equivalent to {converted_amount:.2f} {to_currency}'
        else:
            result_text = 'Currency conversion failed.'
    elif intent == 'help':
        result_text = "Available commands:\n- Get stock price: 'What is the current price of [symbol]?'"
        result_text += "\n- Calculate compound interest: 'Calculate compound interest'"
        result_text += "\n- Calculate simple interest: 'Calculate simple interest'"
        result_text += "\n- Convert currency: 'Convert [amount] from [currency1] to [currency2]'"
        result_text += "\n- Help: 'Help'"
    elif intent == 'unknown':
        result_text = "I'm sorry, I don't understand. Can you please rephrase?"
    else:
        result_text = "Sorry, I can't help with that."

    # Display result in chat window
    chat_log.config(state=tk.NORMAL)
    chat_log.insert(tk.END, f'User: {user_input}\n')
    chat_log.insert(tk.END, f'Bot: {result_text}\n\n')
    chat_log.config(state=tk.DISABLED)
    entry.delete(0, tk.END)

# API keys
api_key_stock = 'KRIIF9738C947889'  # Replace with your Alpha Vantage API key
api_key_currency = '3cef1ab132-b65c8091e8-sj8u1j'  # Replace with your ExchangeRate-API key

# Create GUI
root = tk.Tk()
root.title("Financial Chatbot")

# Create and place widgets
chat_log = tk.Text(root, state=tk.DISABLED, wrap=tk.WORD, height=20, width=60)
chat_log.pack(padx=10, pady=10)

entry = tk.Entry(root, width=60)
entry.pack(side=tk.LEFT, padx=10, pady=10)

send_button = tk.Button(root, text="Send", command=handle_user_input)
send_button.pack(side=tk.RIGHT, padx=10, pady=10)

# Start GUI event loop
root.mainloop()
