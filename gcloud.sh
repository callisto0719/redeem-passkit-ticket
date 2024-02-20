#!/bin/bash

source .env

case $1 in
    deploy-funtion)
        gcloud functions deploy $FUNCTION_NAME \
            --region $REGION \
            --gen2 \
            --runtime php82 \
            --source ./src \
            --trigger-http \
            --entry-point $FUNCTION_NAME \
            --allow-unauthenticated \
            --memory 256Mi \
            --timeout 60s \
            --min-instances 1 \
            --max-instances 5 \
            --set-env-vars SLACK_WEBHOOK_URL=$SLACK_WEBHOOK_URL
        ;;
    delete-function)
        gcloud functions delete $FUNCTION_NAME \
            --region $REGION
        ;;
    run-local)
        FUNCTION_TARGET=$FUNCTION_NAME \
        SLACK_WEBHOOK_URL=$SLACK_WEBHOOK_URL \
        composer start-redeem-ticket \
            --working-dir=./src
    ;;
esac