from flask import Flask, request, jsonify, render_template
import requests
import nltk
from nltk.tokenize import word_tokenize

# Download NLTK data
nltk.download('punkt')

app = Flask(__name__)

api_key_stock = 'MMA59VFVOI1Q68D8'  # Replace with your Alpha Vantage API key
api_key_currency = 'd6a3947a5578b24ea3c5ce43'  # Replace with your ExchangeRate-API key

def get_stock_price(symbol, api_key):
    url = f'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol={symbol}&apikey={api_key}'
    try:
        response = requests.get(url)
        data = response.json()
        price = data['Global Quote']['05. price']
        return float(price)
    except Exception as e:
        return f"Error fetching data: {e}"

def calculate_compound_interest(principal, rate, time):
    amount = principal * (1 + rate / 100) ** time
    interest = amount - principal
    return amount, interest

def calculate_simple_interest(principal, rate, time):
    interest = (principal * rate * time) / 100
    amount = principal + interest
    return amount, interest

def convert_currency(amount, from_currency, to_currency, api_key):
    url = f'https://v6.exchangerate-api.com/v6/{api_key}/pair/{from_currency}/{to_currency}/{amount}'
    try:
        response = requests.get(url)
        data = response.json()
        if data['result'] == 'success':
            converted_amount = data['conversion_result']
            return converted_amount
        else:
            return f"Error: {data['error-type']}"
    except Exception as e:
        return f"Error fetching data: {e}"

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

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/chat', methods=['POST'])
def chat():
    user_input = request.json.get('message')
    intent = determine_intent(user_input)

    if intent == 'get_stock_price':
        stock_symbol = request.json.get('stock_symbol')
        price = get_stock_price(stock_symbol, api_key_stock)
        response = f'Current price of {stock_symbol}: ${price:.2f}'
    elif intent == 'calculate_compound_interest':
        principal = float(request.json.get('principal'))
        annual_rate = float(request.json.get('annual_rate'))
        years = int(request.json.get('years'))
        final_amount, interest = calculate_compound_interest(principal, annual_rate, years)
        response = f'After {years} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})'
    elif intent == 'calculate_simple_interest':
        principal = float(request.json.get('principal'))
        annual_rate = float(request.json.get('annual_rate'))
        years = int(request.json.get('years'))
        final_amount, interest = calculate_simple_interest(principal, annual_rate, years)
        response = f'After {years} years, final amount: ${final_amount:.2f} (Interest earned: ${interest:.2f})'
    elif intent == 'convert_currency':
        amount = float(request.json.get('amount'))
        from_currency = request.json.get('from_currency')
        to_currency = request.json.get('to_currency')
        converted_amount = convert_currency(amount, from_currency, to_currency, api_key_currency)
        response = f'{amount} {from_currency} is equivalent to {converted_amount:.2f} {to_currency}'
    elif intent == 'help':
        response = "Available commands: Get stock price, Calculate compound interest, Calculate simple interest, Convert currency, Help"
    else:
        response = "I'm sorry, I don't understand. Can you please rephrase?"

    return jsonify({'response': response})

if __name__ == "__main__":
    app.run(debug=True)
